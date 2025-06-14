import sys
import json
import pulp

# Define problem parameters
n = 4  # Number of groups (runner groups)
m = 2  # Number of bins (hunters)
group_weights = [2,2,2,1]  # Weights of the groups, how many runners there are in each group
bin_capacity_values = [0, 4, 5, 6]  # Allowed bin capacities (runners per match)


time_units = "minutes"                    # choose either "minutes" or "seconds"
runner_group_queue_times = [1,2,1,3]      # how long each runner group has been waiting
hunter_queue_times = [1,2]                # how long each hunter has been waiting


# duos and trios can afford to wait a bit longer
group_time_multiplier = {    
    1 : 1,
    2 : 0.9,
    3 : 0.8
}

print(sys.argv[1])
print(json.loads(sys.argv[1])['test'])
exit()

if len(group_weights) != n:
    raise ValueError("Number of Runner groups must match number of group weights")
elif len(runner_group_queue_times) != n:
    raise ValueError("All Runner groups must have their queue time indicated")
elif len(hunter_queue_times) != m:
    raise ValueError("All Hunters must have their queue time indicated")

if time_units not in ["minutes","seconds"]:
    raise ValueError("Queue times written in unsupported units")

elif time_units == "seconds":       # rescale from seconds to minutes
    runner_group_queue_times = [time / 60 for time in runner_queue_times]
    hunter_queue_times = [time / 60 for time in hunter_queue_times]

# Scale the queue time per number of people in the runner group and the group-size-dependant scaling factor
runner_queue_times = []
for i, time in enumerate(runner_group_queue_times):
    runner_queue_times.append(runner_group_queue_times[i] * group_weights[i] * group_time_multiplier[group_weights[i]])


# Create a linear programming problem (maximization)
prob = pulp.LpProblem("Bin_Packing", pulp.LpMaximize)

# Define decision variables
x = pulp.LpVariable.dicts("x", (range(n), range(m)), cat='Binary')                              # x[i][j] = 1 if runner group i gets a match with hunter j
y = pulp.LpVariable.dicts("y", range(m), cat='Binary')                                          # y[j] = 1 if hunter j gets a match
y_values = pulp.LpVariable.dicts("y_values", (range(m), bin_capacity_values), cat='Binary')     # y_values[j][v] = 1 if hunter j is matched with v runners
z = pulp.LpVariable.dicts("z", range(m), lowBound=0, cat='Continuous')                          # z[j] is the number of runners assigned to hunter j
unassigned_runners = pulp.LpVariable.dicts("unassigned_runners", range(n), cat='Binary')        # unassigned_runners[i] = 1 if runner group i does not get matched with any hunter


# Objective function: maximize the total weight and number of non-zero weight bins
# Minimize queue times of unassigned players
prob += pulp.lpSum([z[j] + y[j] for j in range(m)]) - pulp.lpSum([runner_queue_times[i] * unassigned_runners[i] for i in range(n)]) + pulp.lpSum([hunter_queue_times[j] * (y[j]-1) for j in range(m)]), "Objective"


# Constraint 1: Each group is assigned to at most one bin
for i in range(n):
    prob += pulp.lpSum([x[i][j] for j in range(m)]) <= 1, f"Group_{i}_Assignment"

# Constraint 2: Bin capacity (total weight in each bin)
for j in range(m):
    prob += z[j] == pulp.lpSum([group_weights[i] * x[i][j] for i in range(n)]), f"Bin_{j}_Weight"

# Constraint 3: Ensure each bin has a total weight in {0, 4, 5, 6}
for j in range(m):
    prob += pulp.lpSum([y_values[j][v] for v in bin_capacity_values]) == 1, f"Bin_{j}_Capacity_Selection"
    prob += z[j] == pulp.lpSum([v * y_values[j][v] for v in bin_capacity_values]), f"Bin_{j}_Total_Weight"

# Constraint 4: Bin is used if the weight is greater than 0
for j in range(m):
    prob += y[j] >= pulp.lpSum([y_values[j][v] for v in bin_capacity_values[1:]]), f"Bin_{j}_Used"   # Hunter j has a match if y[j] = 1

# Constraint 5: Each bin is used if any group is assigned to it
for i in range(n):
    for j in range(m):
        prob += y[j] >= x[i][j], f"Bin_{j}_Used_if_Group_{i}_Assigned"

# Constraint 6: Unassigned runners are not assigned to any bin
for i in range(n):
    prob += unassigned_runners[i] >= 1 - pulp.lpSum([x[i][j] for j in range(m)]), f"Unassigned_{i}_Logic"

# Constraint 7: A hunter with a match cannot have 0 runners
for j in range(m):
    prob += y[j] <= z[j]


# Solve the problem
prob.solve()

# Output the results
print(f"Status: {pulp.LpStatus[prob.status]}")

print("")
print("Hunters:",m)
print("Hunter queue times:", hunter_queue_times,"minutes")
print("Runner Groups:",group_weights,"-",sum(group_weights),"Runners in total")
print("Runner Group queue times:",runner_group_queue_times,"minutes")
print("Scaled Runner queue times:",runner_queue_times,"minutes")
print("")


if m == 0:
    print("There are no Hunters in the queue. No Match can be constructed.")
    print("Number of Runners in the queue:",sum(group_weights))

elif sum(group_weights) < 4:
    print("There are only",sum(group_weights),"Runners in the queue. No Match can be constructed.")
    print("Number of Hunters in the queue:",m)

else:

    # Track matched and unmatched runners
    unmatched_runners = 0
    for i in range(n):
        matched = any(pulp.value(x[i][j]) == 1 for j in range(m))
        if not matched:
            unmatched_runners += group_weights[i]

    # Print match assignments
    unmatched_hunters = 0
    for j in range(m):
        if pulp.value(y[j]) == 1:
            if pulp.value(z[j]) != 0:
                print(f"Hunter {j+1} [queued for {hunter_queue_times[j]} minutes] gets a 1v{int(pulp.value(z[j]))} Match with {int(pulp.value(z[j]))} Runners")
                for i in range(n):
                    if pulp.value(x[i][j]) == 1:
                        print(f"  Runner Group {i+1} [{group_weights[i]} Runners, queued for {runner_group_queue_times[i]} minutes] is assigned to Hunter {j+1}")
            else:
                # raise ValueError ("Hunter without a Match gets Runners assigned to them")
                pass
        else:
            print(f"Hunter {j+1} [queued for {hunter_queue_times[j]} minutes] does not get a Match")
            unmatched_hunters += 1

    print("")
    for i in range(n):
        if pulp.value(unassigned_runners[i]) == 1:
            print(f"Runner Group {i+1} [{group_weights[i]} Runners, queued for {runner_group_queue_times[i]} minutes] does not get a Match")

    print("")
    # Print unassigned runners
    print(f"Number of Runners without a Match: {unmatched_runners}")
    print(f"Number of Hunters without a Match: {unmatched_hunters}")

    print("")
    total_unassigned_queue_time_runners = sum(runner_group_queue_times[i] * group_weights[i] for i in range(n) if pulp.value(unassigned_runners[i]) == 1)
    print(f"Total queue time of unassigned runners: {total_unassigned_queue_time_runners} minutes")
    total_unassigned_queue_time_hunters = sum(hunter_queue_times[j] for j in range(m) if pulp.value(y[j]) == 0)
    print(f"Total queue time of unmatched hunters: {total_unassigned_queue_time_hunters} minutes")
    total_unassigned_queue_time = total_unassigned_queue_time_hunters + total_unassigned_queue_time_runners
    print(f"Total queue time of all unmatched players: {total_unassigned_queue_time} minutes")

# print("Decision Variables:")
# for j in range(m):
#     print(f"Bin {j + 1}:")
#     print(f"  Total weight (z[j]): {pulp.value(z[j])}")
#     print(f"  Used (y[j]): {pulp.value(y[j])}")
#     for i in range(n):
#         print(f"    Group {i + 1} assigned: {pulp.value(x[i][j])}")